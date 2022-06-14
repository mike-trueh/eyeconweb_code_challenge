<?php

namespace App\Http\Controllers\Api;

use App\Events\User\UserCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        event(new UserCreatedEvent($user));

        return $this->returnSuccess();
    }

    /**
     * Display users domains list
     *
     * @param User $user
     * @return JsonResponse
     */
    public function domains(User $user): JsonResponse
    {
        $domains = $user->domains()->paginate(Domain::PER_PAGE);

        return response()->json($domains);
    }
}
