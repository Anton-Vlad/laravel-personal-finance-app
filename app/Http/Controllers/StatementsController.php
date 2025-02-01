<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStatement;
use App\Models\Statement;
use App\Models\Transaction;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $statements = collect([]);
        return Inertia::render('Statements/Index', [
            'data' => $statements,
            'filters' => [],
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function new(Request $request): Response
    {
        return Inertia::render('Statements/UploadStatements', [

        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function upload(StoreStatement $request): JsonResponse
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
                                ]);

                                $processedFiles[] = $filename;
                            }

                            // Clean up temp file
                            @unlink($tempFile);
                        }
                    }

                    $zip->close();
                    @unlink($tempPath);

                    return response()->json(['message' => 'Zip file processed']);
//                    return redirect()->route('statements.index')
//                        ->with('success', count($processedFiles) . ' statements were successfully uploaded');
                }

                return response()->json(['message' => 'Could not process zip file']);
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
            ]);

            return response()->json(['message' => 'Success']);
//            return redirect()
//                ->route('statements.index')
//                ->with('success', 'Statement uploaded successfully');

        } catch (\Exception $e) {
            \Log::error('Statement upload failed: ' . $e->getMessage());
            return response()->json(['message' => 'Upload failed. Please try again']);
//            return redirect()->back()->with('error', 'Upload failed. Please try again.');
        }
    }
}
