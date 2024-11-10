import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable, of, switchMap } from 'rxjs';
import { SeatBookType } from '../book-a-seat-modal/book-a-seat-modal.component';

@Injectable({
  providedIn: 'root'
})
export class ZendeskService {
  // private readonly vumiZendeskEmail = "ixion.chowdhury@brainstation-23.com";
  // private readonly vumiZendeskBaseUrl = "https://brainstation23plcsupport.zendesk.com";
  // private readonly vumiZendeskAPIToken = "sV853Oc70bkgbmgkJqmDVKeuony1jcNrt8UgXm8I";
  // private readonly vumiZendeskPassword = "PassVumi1";

  private readonly vumiZendeskEmail = "vumi.edtech@gmail.com";
  private readonly vumiZendeskBaseUrl = "https://vumibangladeshlimited.zendesk.com";
  private readonly vumiZendeskAPIToken = "HVVrYevu5ZwFUnqo8gEIfKkuRxjWSgiqX7cDzm3g";

  constructor(private http: HttpClient,) { }

  // this creates a ticket and a new user as well(if such a user doesn't exist) in zendesk
  createTicket(info:SeatBookType): Observable<any> {
    // console.log("btoa(this.zendeskEmail + '/token:' + this.zendeskAPIToken)",btoa(this.vumiZendeskEmail + '/token:' + this.vumiZendeskAPIToken));

    // vumi's base64 encoded: dnVtaS5lZHRlY2hAZ21haWwuY29tL3Rva2VuOkhWVnJZZXZ1NVp3RlVucW84Z0VJZktrdVJ4aldTZ2lxWDdjRHptM2c=

    // my base64 encoded: aXhpb24uY2hvd2RodXJ5QGJyYWluc3RhdGlvbi0yMy5jb20vdG9rZW46c1Y4NTNPYzcwYmtnYm1na0pxbURWS2V1b255MWpjTnJ0OFVnWG04SQ==

    const headers = new HttpHeaders({
      'Content-Type': 'application/json',
      // 'Authorization': 'Basic ' + btoa(this.zendeskEmail + ':' + this.zendeskPassword)
      'Authorization': 'Basic ' + btoa(this.vumiZendeskEmail + '/token:' + this.vumiZendeskAPIToken)
    });

    const requestBody = 
    {
      "ticket":{
        "subject": "FROM THE MINDS OF THE EXPERTS",
        "comment": {
          "body": `Dear ${info.name}, this is to let you know that your request for booking a 
        seat has been received succesfully. \n \n Regards,\n VUMI Bangladesh Ltd.`
        },
        "requester": { "email": `${info.email}`, "name": `${info.name}` }
      }
    }
      


    return this.http
      .post(this.vumiZendeskBaseUrl+'/'+'api/v2/tickets', requestBody, {headers:headers})
      .pipe(
        switchMap((response: any) => {
          return of(response);
        })
      );
  }
}
