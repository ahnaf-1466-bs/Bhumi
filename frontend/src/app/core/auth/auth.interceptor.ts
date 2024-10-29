import {
    HttpErrorResponse,
    HttpEvent,
    HttpHandler,
    HttpInterceptor,
    HttpRequest,
    HttpResponse,
} from '@angular/common/http';
import { Injectable } from '@angular/core';
import { AuthService } from 'app/core/auth/auth.service';
import { AuthUtils } from 'app/core/auth/auth.utils';
import { Observable, catchError, of, throwError } from 'rxjs';
import { tap } from 'rxjs/operators';

@Injectable()
export class AuthInterceptor implements HttpInterceptor {
    private cache = new Map<string, any>();

    /**
     * Constructor
     */
    constructor(private _authService: AuthService) {}

    /**
     * Intercept
     *
     * @param req
     * @param next
     */
    intercept(
        req: HttpRequest<any>,
        next: HttpHandler
    ): Observable<HttpEvent<any>> {
        // Clone the request object
        let newReq = req.clone();

        // Request
        //
        // If the access token didn't expire, add the Authorization header.
        // We won't add the Authorization header if the access token expired.
        // This will force the server to return a "401 Unauthorized" response
        // for the protected API routes which our response interceptor will
        // catch and delete the access token from the local storage while logging
        // the user out from the app.
        if (
            this._authService.accessToken &&
            !AuthUtils.isTokenExpired(this._authService.accessToken)
        ) {
            newReq = req.clone({
                headers: req.headers.set(
                    'Authorization',
                    'Bearer ' + this._authService.accessToken
                ),
            });
        }

        // Check if it's a GET request and if it's cached
        if (newReq.method === 'GET') {
            const cachedResponse = this.cache.get(newReq.url);
            if (cachedResponse) {
                return of(cachedResponse);
            }
        }

        // Response
        return next.handle(newReq).pipe(
            tap((event) => {
                if (event instanceof HttpResponse) {
                    this.cache.set(newReq.url, event);
                }
            }),
            catchError((error) => {
                // Catch "401 Unauthorized" responses
                if (
                    error instanceof HttpErrorResponse &&
                    error.status === 401
                ) {
                    // Sign out
                    this._authService.signOut();

                    // Reload the app
                    location.reload();
                }

                return throwError(error);
            })
        );
    }
}
