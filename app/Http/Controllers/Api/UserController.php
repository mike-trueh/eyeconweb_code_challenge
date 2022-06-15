<?php

namespace App\Http\Controllers\Api;

use App\Events\User\UserCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/v1/user",
     *     tags={"Users"},
     *     summary="Create new user",
     *     @OA\Parameter(in="query", required=false, name="name", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", required=true, name="email", @OA\Schema(type="string", format="email")),
     *     @OA\Parameter(in="query", required=false, name="cloudflare_token", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", required=false, name="cloudflare_api_key", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *      response=422,
     *     description="Validation error"
     * )
     * )
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        event(new UserCreatedEvent($user));

        return response()->json($user);
    }

    /**
     * Display users domains list
     *
     * @OA\Get(
     *     path="/api/v1/user/{userId}/domains",
     *     tags={"Users"},
     *     summary="Get domains for current user from DB",
     *     @OA\Parameter(
     *     description="User id",
     *     in="path",
     *     name="userId",
     *     required=true,
     *     @OA\Schema(type="integer")
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
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
