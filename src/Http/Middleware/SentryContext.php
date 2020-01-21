<?php

namespace App\Http\Middleware;

use App\Exceptions\RestApiException;
use App\Models\Account;
use App\Models\Profile;
use Carbon\Carbon;
use Closure;
use Sentry\State\Scope;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;

/**
 * Class SentryContext.
 *
 * @codeCoverageIgnore
 */
class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!app()->environment('testing') && app()->bound('sentry')) {

            $sentry = app('sentry');

            try {
                if (auth()->check()) {
                    /** @var Account $account */
                    $account = auth()->user();

                    // Add user context
                    $sentry->configureScope(function (Scope $scope) use ($account) : void {
                        $scope->setUser([
                            'id'           => $account->getKey(),
                            'username'     => $account->username,
                            'type'         => $account->isAdmin() ? 'admin' : 'user',
                            'account_type' => $account->profile instanceof Profile
                                ? $account->profile->account_type
                                : null,
                            'created_dt'   => Carbon::parse($account->{$account->getCreatedAtColumn()})
                                ->toDateTimeString(),
                        ], true);
                    });
                }
            } catch (TokenBlacklistedException $e) {
                // just move along
            } catch (JWTException $e) {
                // keep moving along
            }  catch (RestApiException $e) {
                // Rest Api exceptions should not be reported for now
            }
        }

        return $next($request);
    }
}