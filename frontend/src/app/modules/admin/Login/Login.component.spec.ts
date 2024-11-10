import { async, TestBed } from "@angular/core/testing";
import { LoginComponent } from "./Login.component";
import { FormBuilder, UntypedFormBuilder } from '@angular/forms';
import { Router } from "@angular/router";
import { AuthService } from "app/core/auth/auth.service";

const _authService = jasmine.createSpyObj('AuthService', ['signIn']);
const _router = jasmine.createSpyObj('Router', ['navigate']);
// const _formBuilder = jasmine.createSpyObj('UntypedFormBuilder');

describe('LoginComponent' , () => {
    beforeEach(async(() => {
        TestBed.configureTestingModule({
          declarations: [
            LoginComponent
          ],
        }).compileComponents();
      }));

      it('should create the app', async(() => {
        const component = new LoginComponent(new UntypedFormBuilder,_router,_authService);
        // const login = fixture.debugElement.componentInstance;
        expect(component).toBeTruthy();
      }));
})