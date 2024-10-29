import { Component, ViewChild } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { GetProfileService } from '../services/get-profile.service';
import { ResetPasswordService } from '../../password/services/reset-password.service';
import { Router } from '@angular/router';
import { FormGroup, NgForm, UntypedFormBuilder, UntypedFormGroup, Validators } from '@angular/forms';



function matchPassword(control: FormGroup) {
  const password = control.get('password');
  const confirmPassword = control.get('confirmPassword');

  if (password.value !== confirmPassword.value) {
    confirmPassword.setErrors({ matchPassword: true });
  } else {
    confirmPassword.setErrors(null);
  }
}



@Component({
  selector: 'app-reset-password',
  templateUrl: './reset-password.component.html',
  styleUrls: ['./reset-password.component.scss']
})
export class ResetPasswordComponent {

 
  loading:boolean = false;
  
  passMatched:boolean = true;
  validPassword:boolean = true;
  validToken:boolean=true;
  confirmedUser:boolean = true;

  showPassword: boolean = false;
  showConfirmPassword: boolean = false;
  token:string;
  email:string;
  userID:string;

  newPassword:string;

 
  @ViewChild('resetPassForm') regForm: NgForm;
    formInfo: UntypedFormGroup;

  constructor(
    private _formBuilder: UntypedFormBuilder,
    private route: ActivatedRoute,
    private getProfileService: GetProfileService,
    private resetPassService: ResetPasswordService,
    private _router:Router
  ){}

  ngOnInit(){

    this.loading=false;

    this.formInfo = this._formBuilder.group({
   
      password  : ['',  Validators.compose ([Validators.required, Validators.minLength(8), 
        Validators.pattern('^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[$@$!%*?&])[A-Za-z\\d$@$!%*?&].{7,}$')
      ])],
      confirmPassword: ['', Validators.compose ([Validators.required, Validators.minLength(8)])]
    }, {
      validator: matchPassword
    });

    this.validToken=true;
      this.passMatched = true;
      this.validPassword = true;

      this.route.queryParams.subscribe(params => {
         
            this.token = params.token;
            this.email = params.email;
          
      }
    );
      
  
  }

  togglePasswordVisibility() {
    this.showPassword = !this.showPassword;
 }

 toggleConfirmPasswordVisibility() {
    this.showConfirmPassword = !this.showConfirmPassword;
 }

  onSubmit(){
   
      this.validPassword = true; 
      
      let userData = this.formInfo.value; 

      this.newPassword = userData.password;
        
      this.loading = true;

      const reqPayload:any = {
          email: this.email,
          token: this.token,
          password: this.newPassword    
      }

              
      this.resetPassService.updatePassword(reqPayload).subscribe( (successRes:any)=>{
       
            if( successRes.status == false ){
                  this.loading = false;
                  this.validToken = false;
            }
            else{
                this._router.navigate(['/password/update']);
            }
                          
      })
              
  }



}