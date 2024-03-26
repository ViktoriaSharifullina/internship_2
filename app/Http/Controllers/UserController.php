<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AuthorizeRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Register"},
     *     summary="Регистрация нового пользователя",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Данные нового пользователя",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="YourSecureP@ssw0rd")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Успешная регистрация",
     *         @OA\JsonContent(
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="password_check_status", type="string", example="good")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Неверный запрос / Недостаточно данных"
     *     ),
     *      @OA\Response(
     *         response=422,
     *         description="Ошибка валидации"
     *     )
     * )
     */

    public function register(RegisterRequest $request)
    {
        $userData = $request->validated();
        $registrationResult = $this->userService->register($userData);

        return response()->json([
            'user_id' => $registrationResult['user']->id,
            'password_check_status' => $registrationResult['password_check_status']
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/authorize",
     *     summary="Авторизация пользователя",
     *     tags={"Authorize"},
     *     description="Авторизует пользователя и возвращает токен доступа JWT и user_id.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Данные для авторизации пользователя",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="YourSecureP@ssw0rd")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная авторизация",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", description="Токен доступа JWT"),
     *             @OA\Property(property="user_id", type="integer", description="Идентификатор пользователя")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Сообщение об ошибке")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Детальное сообщение об ошибке валидации")
     *         )
     *     )
     * )
     */
    public function authenticate(AuthorizeRequest $request): JsonResponse
    {
        $result = $this->userService->authorize($request->email, $request->password);

        return response()->json([
            'access_token' => $result['token'],
            'user_id' => $result['user_id']
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/feed",
     *     summary="Получение ленты новостей",
     *     tags={"Feed"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный доступ",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Access token is valid."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Неавторизован - невалидный токен доступа",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized."),
     *         )
     *     )
     * )
     */
    public function feed(Request $request)
    {
        return response()->json(['message' => 'Access token is valid.'], Response::HTTP_OK);
    }
}
