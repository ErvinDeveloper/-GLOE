<?php

namespace App\Http\Controllers;

use App\Actions\AudioPlayer;
use App\Actions\CheckingAnswerForCorrectness;
use App\Actions\EditorJS;
use App\Actions\VideoPlayer;
use App\Actions\YoutubeIframe;
use App\Http\Requests\Testing\ReplyRequest;
use App\Models\Question;
use App\Models\Test;
use App\Repository\QuestionRepository;
use App\Repository\TestingRepository;
use App\Repository\UserRepository;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TestingController extends Controller
{
    use Response;

    public function __construct(
        private readonly TestingRepository  $testingRepository,
        private readonly QuestionRepository $questionRepository,
        private readonly UserRepository     $userRepository
    )
    {
    }

    public function show(
        Test     $test,
        Question $question
    ): View
    {
        $question = $this->questionRepository->get($test, $question);
        $myAnswers = $this->userRepository->myAnswers($test);
        return view('testing.question.' . $question->question_type,
            compact('test', 'question', 'myAnswers'));
    }

    public function check(
        Test                         $test,
        Question                     $question,
        ReplyRequest                 $request,
        CheckingAnswerForCorrectness $action
    ): JsonResponse
    {
        $question->doesQuestionRelateToCurrentTesting($test);

        $result = $action->handler($test, $question, $request);

        if ($result === true) {
            return $this->responseSuccess([
                'message' => 'Отличный ответ! Так держать',
                'nextQuestionLink' => $action->getNextQuestionLink($test)
            ]);
        }

        return $result;

    }

    public function reset(): RedirectResponse
    {
        $this->userRepository->resetAnswereds();
        return redirect()->route('home');
    }

    public function again(Question $question): RedirectResponse
    {
        $this->userRepository->againAnswered($question);
        return redirect()->back();
    }
}
