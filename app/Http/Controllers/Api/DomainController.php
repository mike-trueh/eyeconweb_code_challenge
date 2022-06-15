<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class DomainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/v1/domain",
     *     tags={"Domains"},
     *     summary="Get all domains for DB",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $domains = Domain::query()->paginate(Domain::PER_PAGE);

        return response()->json($domains);
    }
}
