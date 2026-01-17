<?php

namespace KafeinStudio\EmailPreview;


use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Filesystem\Filesystem;

class EmailPreviewController
{
    public function __construct()
    {
        if (!empty(config('emailpreview.allowedIps')) && !in_array($_SERVER['REMOTE_ADDR'], config('emailpreview.allowedIps'))) {
            abort(401);
        }
    }


    public function list(): string
    {
        $this->cleanOldPreviews();

        $emails = glob(config('emailpreview.path') . '/*.html');

        return view('laravel-email-preview::directory_listing', compact('emails'));
    }


    public function show(string $emailName): string
    {
        return file_get_contents(config('emailpreview.path') . '/' . $emailName . '.html');
    }


    public function download(string $emailName): string
    {
        $file = config('emailpreview.path') . '/' . $emailName . '.eml';

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }

        abort(404);
    }


    private function cleanOldPreviews(): void
    {
        try {
            $files = app()->make(Filesystem::class);
        } catch (BindingResolutionException $e) {
            return;
        }

        $oldPreviews = array_filter($files->files(config('emailpreview.path')), function ($file) use ($files) {
            return time() - $files->lastModified($file) > config('emailpreview.lifeTime');
        });

        if ($oldPreviews) {
            $files->delete($oldPreviews);
        }
    }
}
