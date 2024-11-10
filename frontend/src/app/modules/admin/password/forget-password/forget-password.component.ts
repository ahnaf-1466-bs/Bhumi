import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { ForgetPasswordService } from '../services/forget-password.service';
import { GetProfileService } from '../../Login/services/get-profile.service';

@Component({
  selector: 'app-forget-password',
  templateUrl: './forget-password.component.html',
  styleUrls: ['./forget-password.component.scss']
})
export class ForgetPasswordComponent {

  accountConfirmed:boolean = true;
  loading:boolean = false;
  emailInfo = {
    email:"",
  }

 constructor(
     private _router:Router,
     private forgetPassService:  ForgetPasswordService,

 ){}


 ngOnInit(){
      this.loading = false;
 }

onSubmit(){

     this.loading = true;
  
      this.forgetPassService.reqPassReset(this.emailInfo.email).subscribe( (successRes:any)=>{
            this._router.navigate(['password/retrieve/action'])
      })
                
 
    }
}
