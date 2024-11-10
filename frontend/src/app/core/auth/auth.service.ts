import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { catchError, map, Observable, of, switchMap, throwError } from 'rxjs';
import { AuthUtils } from 'app/core/auth/auth.utils';
import { UserService } from 'app/core/user/user.service';
import { environment } from 'environments/environment';
import { AuthMockApi } from 'app/mock-api/common/auth/api';

@Injectable()
export class AuthService
{
    private _authenticated: boolean = false;

    /**
     * Constructor
     */
    constructor(
        private _httpClient: HttpClient,
        private _userService: UserService,
        private _api: AuthMockApi
    )
    {
    }

    // -----------------------------------------------------------------------------------------------------
    // @ Accessors
    // -----------------------------------------------------------------------------------------------------

    /**
     * Setter & getter for access token
     */
    set accessToken(token: string)
    {
        localStorage.setItem('accessToken', token);
    }

    get accessToken(): string
    {
        return localStorage.getItem('accessToken') ?? '';
    }

    // -----------------------------------------------------------------------------------------------------
    // @ Public methods
    // -----------------------------------------------------------------------------------------------------

    /**
     * Forgot password
     *
     * @param email
     */
    forgotPassword(email: string): Observable<any>
    {
        return this._httpClient.post('api/auth/forgot-password', email);
    }

    /**
     * Reset password
     *
     * @param password
     */
    resetPassword(password: string): Observable<any>
    {
        return this._httpClient.post('api/auth/reset-password', password);

    }

    logIn(credentials: any){
        if ( this._authenticated )
        {
            return throwError('User is already logged in.');
        }
        let httpParams = new HttpParams().set('username',credentials.username).set('password' , credentials.password).set('service' , credentials.service);

        return this._httpClient.post(`${environment.baseURL}/login/token.php`, httpParams).pipe(
            switchMap((response: any) => {

                // Store the access token in the local storage
                if(response.token){

                    const gen = this._api._generateJWT();
                    this.accessToken = gen;
                    localStorage.setItem('userToken' , response.token);
                    localStorage.setItem('auth-token' , response.token);
                    // Set the authenticated flag to true
                    this._authenticated = true;
                    
                    
                    return of(response);
                }
                throw Error(response.message);
            })
        );

        

    }

    /**
     * Sign in
     *
     * @param credentials
     */
    signIn(credentials: { username: string; password: string , service: string}): Observable<any>
    {
        // Throw error, if the user is already logged in
        if ( this._authenticated )
        {
            return throwError('User is already logged in.');
        }

        

        return this._httpClient.post('', credentials).pipe(
            switchMap((response: any) => {

                // Store the access token in the local storage
                this.accessToken = response.token;

                // Set the authenticated flag to true
                this._authenticated = true;

                // Store the user on the user service
                this._userService.user = response.user;

                // Return a new observable with the response
                return of(response);
            })
        );
    }

    /**
     * Sign in using the access token
     */
    // signInUsingToken(): Observable<any>
    // {
    //     // Sign in using the token
    //     return this._httpClient.post('api/auth/login-with-token', {
    //         accessToken: this.accessToken
    //     }).pipe(
    //         catchError(() =>

    //             // Return false
    //             of(false)
    //         ),
    //         switchMap((response: any) => {

    //             // Replace the access token with the new one if it's available on
    //             // the response object.
    //             //
    //             // This is an added optional step for better security. Once you sign
    //             // in using the token, you should generate a new one on the server
    //             // side and attach it to the response object. Then the following
    //             // piece of code can replace the token with the refreshed one.
    //             if ( response.accessToken )
    //             {
    //                 this.accessToken = response.accessToken;
    //             }

    //             // Set the authenticated flag to true
    //             this._authenticated = true;

    //             // Store the user on the user service
    //             this._userService.user = response.user;

    //             // Return true
    //             return of(true);
    //         })
    //     );
    // }

    /**
     * Sign out
     */
    signOut(): Observable<any>
    {
        /* DONOT CLEAR ENTIRETY OF LOCALSTORAGE */
        localStorage.removeItem('user-id');
        localStorage.removeItem('userToken');
        localStorage.removeItem('user-firstname');
        localStorage.removeItem('accessToken');
        localStorage.removeItem('profile-status');
        localStorage.removeItem('profile-status: ');
        localStorage.removeItem('auth-token');
        localStorage.removeItem('user-fullname');
        localStorage.removeItem('user-mail');
        localStorage.removeItem('moduleType');
        localStorage.removeItem('prev');

        this._userService.setLogin(false);
        this._authenticated = false;
        return of(true);
    }

    /**
     * Sign up
     *
     * @param user
     */
    signUp(user: { name: string; email: string; password: string; company: string }): Observable<any>
    {
        return this._httpClient.post('api/auth/sign-up', user);
    }

    /**
     * Unlock session
     *
     * @param credentials
     */
    unlockSession(credentials: { email: string; password: string }): Observable<any>
    {
        return this._httpClient.post('api/auth/unlock-session', credentials);
    }

    /**
     * Check the authentication status
     */
    check(): Observable<boolean>
    {
        // Check if the user is logged in
        if (this._authenticated) {
            return of(true);
        }

        // Check the access token availability
        if (!this.accessToken) {
            return of(false);
        }

        // Check the access token expire date
        if (AuthUtils.isTokenExpired(this.accessToken)) {
            return of(false);
        }
        return of(true);
        // If the access token exists and it didn't expire, sign in using it
        // return this.signInUsingToken();
    }
}
