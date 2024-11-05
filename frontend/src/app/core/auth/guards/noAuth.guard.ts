import { Injectable } from '@angular/core';
import { CanMatch, Route, Router, UrlSegment, UrlTree } from '@angular/router';
import { Observable, of, switchMap } from 'rxjs';
import { AuthService } from 'app/core/auth/auth.service';

@Injectable({
    providedIn: 'root'
})
export class NoAuthGuard implements CanMatch
{
    /**
     * Constructor
     */
    constructor(
        private _authService: AuthService,
        private _router: Router
    )
    {
    }

    // -----------------------------------------------------------------------------------------------------
    // @ Public methods
    // -----------------------------------------------------------------------------------------------------

    /**
     * Can match
     *
     * @param route
     * @param segments
     */
    canMatch(route: Route, segments: UrlSegment[]): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree
    {
        return this._check(segments);
    }

    // -----------------------------------------------------------------------------------------------------
    // @ Private methods
    // -----------------------------------------------------------------------------------------------------

    /**
     * Check the authenticated status
     *
     * @private
     */
    private _check(segments: UrlSegment[]): Observable<boolean | UrlTree> 
    {
        // Check the authentication status and return an observable of
        // "true" or "false" to allow or prevent the access
        return this._authService.check().pipe(
            switchMap((authenticated) => {
                // If the user is authenticated...
                if (authenticated) {
                    // Redirect to dashboard page
                    this._router.navigate(['/dashboard']);
                }

                // Allow the access
                return of(true);
            })
        );
    }
}