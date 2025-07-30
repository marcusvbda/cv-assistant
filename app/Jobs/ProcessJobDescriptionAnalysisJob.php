<?php

namespace App\Jobs;

use App\Enums\JobDescriptionAnalysisStatusEnum;
use App\Enums\JobDescriptionAnalysisTypeEnum;
use App\Models\JobApplyDetail;
use App\Models\JobDescriptionAnalysis;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DB;

class ProcessJobDescriptionAnalysisJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected JobDescriptionAnalysis $item;
    protected User $user;
    protected string $event;
    protected string $queueType;

    public function __construct(JobDescriptionAnalysis $item, User $user, string $event)
    {
        $this->item = $item;
        $this->event = $event;
        $this->user = $user;
        $this->queueType = config("queue.default");
    }

    public function handle(): void
    {
        if ($this->event === 'start_processing' && $this->queueType === 'database') {
            DB::table('job_description_analyses')->where('id', $this->item->id)->update(['status' => JobDescriptionAnalysisStatusEnum::IN_PROGRESS->name]);
            dispatch(new static($this->item, $this->user, 'processes'));
            return;
        }

        $description = $this->getJobDescriptionText();
        $service = AIService::make()
            ->setUser($this->user)
            ->user(json_encode([
                "job_title" => $this->item->name,
                "job_description" => $description,
                "person_details" => $this->user->getPayloadContext(),
            ]))
            ->user("Using only the job description and personal details provided, generate two Markdown-formatted fields: 'resume' and 'cover_letter'.")
            ->user("Do not add, invent, or assume any information not present in the input.")
            ->user("If there are any grammar issues in the personal details, fix them automatically in the final output.")
            ->user("Dont hide any personal details in the final output and keep the experiences in order of the most recent ones.")
            ->user("if some section have no information just hide it.")
            ->user("dont hide experiences and projects descriptions ( just fix the grammar ).")
            ->user("join certificates and courses together.")
            ->user("format dates to just year and if some item has no end date just put 'present'.")
            ->user("Return only a JSON object with the keys: 'resume' and 'cover_letter', both containing Markdown content with a good design and otimized for ATS, percentage_of_fit(0-100 and comment_about_fit.");

        $result = $service->json([
            "cover_letter" => "...",
            "resume" => "...",
            "percentage_of_fit" => 0,
            "comment_about_fit" => "..."
        ])->generate();

        JobApplyDetail::updateOrCreate(['job_description_analysis_id' => $this->item->id], [
            'resume' => data_get($result, 'resume', ''),
            'cover_letter' => data_get($result, 'cover_letter', ''),
            'percentage_fit' => data_get($result, 'percentage_of_fit', 0),
            'comment' => data_get($result, 'comment_about_fit', ''),
        ]);

        DB::table('job_description_analyses')->where('id', $this->item->id)->update(['status' => JobDescriptionAnalysisStatusEnum::COMPLETED->name]);
    }

    private function getJobDescriptionText()
    {
        if ($this->item->description_type === JobDescriptionAnalysisTypeEnum::JOB_DESCRIPTION->name) {
            return $this->item->description;
        }
        return $this->getJobDescriptionFromUrl($this->item->description);
    }

    private function getJobDescriptionFromUrl(string $url): ?string
    {
        $response = Http::get($url);

        if (!$response->successful()) {
            return null;
        }

        $html = $response->body();

        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);

        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
