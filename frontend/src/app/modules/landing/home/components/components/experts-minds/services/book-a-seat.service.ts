import { Injectable } from '@angular/core';
import { Observable, of, switchMap } from 'rxjs';
import { SeatBookType } from '../book-a-seat-modal/book-a-seat-modal.component';
import { environment } from 'environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class BookASeatService {

  constructor(private http: HttpClient,) { }

  bookSeat(info:SeatBookType): Observable<any> {
    let globalToken = environment.globalToken;

    let formData = new FormData();
    formData.append('wsfunction', 'local_book_seat_store_userinfo');
    formData.append('wstoken', globalToken);
    formData.append('moodlewsrestformat', 'json');
    formData.append('name', info.name);
    formData.append('email', info.email);
    formData.append('phone', info.phone);


    return this.http
      .post(`${environment.baseURL}/webservice/rest/server.php`, formData)
      .pipe(
        switchMap((response: any) => {
          return of(response);
        })
      );
  }
}
