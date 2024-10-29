import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class ProfileCompletionGuard implements CanActivate {

  constructor(
    private _router:Router
  ){}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    if(localStorage.getItem('profile-status: ') == "88924")return true;
    if(localStorage.getItem('auth-token')==undefined || localStorage.getItem('auth-token')==null){
        //unlogged user can always visit any page
        return true;}
    else{
        this._router.navigate(['profile']);
        return false;
    }
  }
  
}
