import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { ConfirmUserService } from '../../services/confirm-user.service';

@Component({
  selector: 'app-registration-confirm',
  templateUrl: './registration-confirm.component.html',
  styleUrls: ['./registration-confirm.component.scss']
})
export class RegistrationConfirmComponent {
  constructor(private route: ActivatedRoute,
    private confirmUser: ConfirmUserService){}
secret:string;
username:string;

ngOnInit() {
      this.route.queryParams.subscribe(params => {
           
            this.secret = params.data;
            this.username = params.email;

                  let payLoad = {
                    secret: this.secret,
                    username: this.username
                  }
                 

                  this.confirmUser.confirmUser(payLoad).subscribe( (successRes:any)=>{
                
                  })


            }
      );
 }

}
