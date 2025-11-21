<?php

namespace App\Http\Middleware;

use App\Models\Log;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Jenssegers\Agent\Agent;

class Logger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $agent = new Agent();
        $agent->setUserAgent($request->header('User-Agent'));
        $deviceType = $agent->device() ?: 'Unknown';
        $browser = $agent->browser() ?: 'Unknown';
        $platform = $agent->platform() ?: 'Unknown';

        $startTime = microtime(true);
        $response = $next($request);
        $user = $request->user();
        $logData = [
            'user_id'     => optional($user)->id,
            'user_name'   => optional($user)->name,
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
            'ip_address'  => $request->ip(),
            'device'      => $deviceType,
            'browser'     => $browser,
            'platform'    => $platform,
            'payload'     => $request->all(),
        ];

        $logData['status_code'] = $response->getStatusCode();

        $path = $request->path();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $skipExtensions = ['css', 'js', 'map', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'ico', 'webp'];

        if (in_array($ext, $skipExtensions)) {
            // return $next($request); // Skip logging for these files
            return $response;
        }
        $contentType = $response->headers->get('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            $logData['response_body'] = json_decode($response->getContent(), true)
                ?? $response->getContent();
        } else {
            $logData['response_body'] = null;
        }

        $logData['execution_time_ms'] = round((microtime(true) - $startTime) * 1000, 2);

        Log::create([
            'user_id'        => $logData['user_id'],
            'user_name'      => $logData['user_name'],
            'method'         => $logData['method'],
            'url'            => $logData['url'],
            'ip_address'     => $logData['ip_address'],
            'device'         => $logData['device'],
            'browser'        => $logData['browser'],
            'platform'       => $logData['platform'],
            'payload'        => json_encode($logData['payload']),
            'status_code'    => $logData['status_code'],
            //    'response_body'  => json_encode($logData['response_body']),
            'execution_time' => $logData['execution_time_ms'],
        ]);

        return $response;
    }
}
