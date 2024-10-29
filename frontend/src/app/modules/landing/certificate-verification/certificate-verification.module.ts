import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { SharedModule } from 'app/shared/shared.module';
import { routes } from './certificate-verification.routing';
import { CertificateVerificationComponent } from './components/certificate-verification/certificate-verification.component';

@NgModule({
    declarations: [CertificateVerificationComponent],
    imports: [SharedModule, RouterModule.forChild(routes)],
})
export class CertificateVerificationModule {}
