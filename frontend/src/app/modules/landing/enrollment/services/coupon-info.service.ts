import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { environment } from 'environments/environment';
import { Observable, of, switchMap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})

@Injectable({
  providedIn: 'root'
})

export class CouponInfoService {

  constructor(
    private http: HttpClient,
    private _router: Router
  ) {}

  verifyDiscount(coupon, courseID):Observable<any>{
      
    
    let formData = new FormData();
    formData.append('wsfunction', 'local_discount_verify_coupon');
    formData.append('wstoken', localStorage.getItem("auth-token"));
    formData.append('moodlewsrestformat', 'json');
    formData.append('userid', localStorage.getItem("user-id"));
    formData.append('courseid', courseID);
    formData.append('coupon_code', coupon);
                        
    return this.http.post(`${environment.baseURL}/webservice/rest/server.php`, formData).pipe(
      switchMap((response: any) => {
              return of(response);
      })
    );
  }
}
