<?php

namespace App\Http\Controllers\Api\V1\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/account/register",
     * operationId="Register",
     * tags={"User"},
     * summary="회원가입",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *              type="object",
     *              title="RegisterForm",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string", example="user@example.com"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="password_confirmation", type="string"),
     *         ),
     *    ),
     *      @OA\Response (
     *          response=201,
     *          description="회원가입 완료",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="처리 불가",
     *          @OA\JsonContent()
     *       ),
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $success['token'] =  $user->createToken('authToken')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['result' => $success], 201);
    }

    /**
     * @OA\Post(
     * path="/account/login",
     * operationId="authLogin",
     * tags={"User"},
     * summary="로그인",
     * description="Login User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *              type="object",
     *              title="LoginForm",
     *              @OA\Property(property="email", type="string", example="user@example.com"),
     *              @OA\Property(property="password", type="string"),
     *      ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="로그인 성공",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="권한이 없음",
     *          @OA\JsonContent()
     *       ),
     * )
     */
    public function login(Request $request)
    {
        $validator = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($validator)) {
            return response()->json([
                'result' => 'error',
                'message' => 'unauthorized client',
            ], 401);
        } else {
            $success['token'] = auth()->user()->createToken('authToken')->accessToken;
            $success['user'] = auth()->user();
            return response()->json(['result' => $success]);
        }
    }
}
