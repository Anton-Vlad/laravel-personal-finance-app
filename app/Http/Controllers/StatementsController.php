<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatement;
use App\Http\Resources\StatementResource;
use App\Models\Statement;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use function Illuminate\Support\defer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class StatementsController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(Request $request): Response
    {
        $current_user_id = Auth::id();
        $statements = Statement::query();

        $statements = $statements->where('user_id', $current_user_id);
        $statements = $statements->orderBy('updated_at', 'desc');

        $data = $statements->paginate(10);

        return Inertia::render('Statements/Index', [
            'data' => StatementResource::collection($data),
            'filters' => [],
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function new(Request $request): Response
    {
        return Inertia::render('Statements/UploadStatements', []);
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        try {
            $current_user_id = Auth::id();
            $statement = Statement::where('user_id', $current_user_id)->findOrFail($id);
            $file_path = $statement->file_path;

            // Also delete the stored file!!
            $statement->delete();

            logger()->info('Statement to delete', [$statement->id]);

            if (Storage::disk('private')->exists($file_path)) {
                Storage::disk('private')->delete($file_path);

                logger()->info('Statement to delete', [$file_path]);
            }

            return redirect()
                ->route('statements.index')
                ->with('message', 'Statement deleted successfully ' . $id);
        } catch (\Exception $e) {
            logger()->error('Statement destroy failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete statement.');
        }
    }

    public function download(Request $request, $id): RedirectResponse
    {
        try {
            $current_user_id = Auth::id();
            $statement = Statement::where('user_id', $current_user_id)->findOrFail($id);

            logger()->info('Download statement', [$statement->id]);



            return redirect()
                ->route('statements.index')
                ->with('message', 'Statement downloaded successfully ' . $id);
        } catch (\Exception $e) {
            logger()->error('Statement download failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download statement.');
        }
    }

    /**
     * Display the user's profile form.
     */
    public function upload(StoreStatement $request): RedirectResponse
    {
        try {
            $file = $request->file('statement');

            if ($file->getClientOriginalExtension() === 'zip') {
                $zip = new \ZipArchive();
                $tempPath = storage_path('app/temp/' . uniqid('zip_'));

                // Store zip temporarily
                $file->move(dirname($tempPath), basename($tempPath));

                if ($zip->open($tempPath) === true) {
                    $processedFiles = [];

                    // Process each file in the zip
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $filename = $zip->getNameIndex($i);
                        $extension = pathinfo($filename, PATHINFO_EXTENSION);

                        // Only process PDF files
                        if (strtolower($extension) === 'pdf') {
                            // Extract to temp location
                            $zip->extractTo(storage_path('app/temp'), $filename);
                            $tempFile = storage_path('app/temp/' . $filename);

                            // Store in private storage
                            $statement_path = Storage::disk('private')->putFileAs(
                                'statements',
                                $tempFile,
                                $filename
                            );

                            if ($statement_path) {
                                Statement::create([
                                    'user_id' => auth()->id(),
                                    'title' => $filename,
                                    'file_path' => $statement_path,
                                    'file_size' => Storage::disk('private')->size($statement_path)
                                ]);

                                $processedFiles[] = $filename;
                            }

                            // Clean up temp file
                            @unlink($tempFile);
                        }
                    }

                    $zip->close();
                    @unlink($tempPath);

                    Artisan::call('parse:statement', ['user' => auth()->id()]);

                    return redirect()->route('statements.index')
                        ->with('message', count($processedFiles) . ' statements were successfully uploaded');
                }

                logger()->error('Could not process zip file');
                return redirect()->back()->with('error', 'Could not process zip file');
            }

            // Handle single file upload
            $statement_path = $file->storeAs(
                'statements',
                $file->getClientOriginalName(),
                'private'
            );

            Statement::create([
                'user_id' => auth()->id(),
                'title' => $file->getClientOriginalName(),
                'file_path' => $statement_path,
                'file_size' => $file->getSize(),
            ]);

            $user_id = auth()->id();

//            defer(fn() => Artisan::call('parse:statement', ['user' => $user_id]));
            Artisan::call('parse:statement', ['user' => $user_id]);


            return redirect()
                ->route('statements.index')
                ->with('message', 'Statement uploaded successfully');

        } catch (\Exception $e) {
            logger()->error('Statement upload failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Upload failed. Please try again.');
        }
    }
}
