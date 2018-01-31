<?php

namespace App\Http\Middleware;

use App\Service\CatalogService;
use App\Service\TokenService;
use Carbon\Carbon;
use Closure;

class Token
{
    private $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader('token')) {
            $token = $this->tokenService->getTokenByContent($request->header('token'));
            if (!$token)
                return response()->json([
                    'code' => 1006,
                    'message' => 'token无效'
                ]);
            $time = new Carbon();
            if ($request->header('token') == $token->token_content && $token->expired_at > $time){
                $userInfo = $this->tokenService->getUserByToken($token->user_id);
            $request->user = $userInfo;
            return $next($request);
            }
        else
            {
                return response()->json([
                    'code' => 1006,
                    'message' => 'token无效'
                ]);
            }
        } else
            return response()->json([
                'code' => 1006,
                'message' => '没有token'
            ]);

    }
}