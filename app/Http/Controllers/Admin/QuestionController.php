<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Question\GetWordsChunkRequest;
use App\Http\Requests\Admin\Question\StoreDefaultRequest;
use App\Http\Requests\Admin\Question\StoreWordsChunkRequest;
use App\Http\Requests\Admin\Question\StoreWordsSubstitute;
use App\Http\Requests\Admin\Question\UpdateDefaultRequest;
use App\Http\Requests\Admin\Question\UpdateWordsChunkRequest;
use App\Http\Requests\Admin\Question\UpdateWordsSubstitute;
use App\Models\Question;
use App\Models\Test;
use App\Repository\QuestionRepository;
use App\Services\QuestionService;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class QuestionController extends Controller
{
    use Response;

    public function __construct(
        private readonly QuestionService    $questionService,
        private readonly QuestionRepository $questionRepository
    )
    {
    }

    public function index(Test $test): View
    {
        return view('admin.question.index', compact('test'));
    }

    public function create(Test $test): View
    {
        return view('admin.question.create', compact('test'));
    }

    public function edit(
        Test $test,
        int  $question
    ): View
    {
        $question = $this->questionRepository->getForEditing($question);
        if (empty($question)) {
            abort(404);
        }

        return view('admin.question.edit', $question);
    }

    public function updateWordsChunk(
        Question                $question,
        UpdateWordsChunkRequest $request
    ): JsonResponse
    {
        $result = $this->questionService->updateWordsChunk($question, $request);
        return $this->returnUpdateMessage($result);
    }

    public function updateWordsSubstitute(
        Question              $question,
        UpdateWordsSubstitute $request
    ): JsonResponse
    {
        $result = $this->questionService->updateWordsSubstitute($question, $request);
        return $this->returnUpdateMessage($result);
    }

    public function updateDefault(
        Question             $question,
        UpdateDefaultRequest $request
    ): JsonResponse
    {
        $result = $this->questionService->updateDefault($question, $request);
        return $this->returnUpdateMessage($result);
    }

    public function storeWordsSubstitute(
        Test                 $test,
        StoreWordsSubstitute $request
    ): JsonResponse
    {
        $result = $this->questionService->storeWordsSubstitute($test, $request);
        return $this->returnCreationMessage($result);
    }

    public function storeWordsChunk(
        Test                   $test,
        StoreWordsChunkRequest $request
    ): JsonResponse
    {
        $result = $this->questionService->storeWordsChunk($test, $request);
        return $this->returnCreationMessage($result);
    }

    public function storeDefault(
        Test                $test,
        StoreDefaultRequest $request
    ): JsonResponse
    {
        $result = $this->questionService->storeDefault($test, $request);
        return $this->returnCreationMessage($result);
    }

    public function deleteVideo(Question $question): JsonResponse
    {
        if ($this->questionService->deleteVideo($question)) {
            return $this->responseSuccess([
                'message' => 'Видео удалено'
            ]);
        }

        return $this->responseError([
            'message' => 'Возникла ошибка при удалении видео'
        ]);
    }

    public function getDefaultOptionHTML(): string
    {
        return view('admin.question.default')->render();
    }

    public function getWordsChunkHTML(GetWordsChunkRequest $request): string
    {
        return $this->questionService->getWordsChunkHTML($request);
    }

    public function getWordsSubstituteHTML(GetWordsChunkRequest $request): string
    {
        return $this->questionService->getWordsSubstituteHTML($request);
    }

    public function delete(Test $test, Question $question): RedirectResponse
    {
        if ($this->questionService->delete($question)) {
            return redirect()
                ->route('admin.question.index', $test)
                ->withWarning('Задание удалено');
        }

        return redirect()
            ->route('admin.question.index', $test)
            ->withErrors('Возникла ошибка при удалении');
    }

    private function returnUpdateMessage(mixed $result): JsonResponse
    {
        if (is_string($result)) {
            return $this->responseError([
                'message' => $result
            ]);
        }

        return $this->responseSuccess([
            'message' => 'Вопрос обновлен'
        ]);
    }

    private function returnCreationMessage(mixed $result): JsonResponse
    {
        if (is_string($result)) {
            return $this->responseError([
                'message' => $result
            ]);
        }

        return $this->responseSuccess([
            'message' => 'Вопрос добавлен'
        ]);
    }
}
