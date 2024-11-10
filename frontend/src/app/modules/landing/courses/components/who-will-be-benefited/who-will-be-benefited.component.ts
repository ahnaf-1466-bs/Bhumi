import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-who-will-be-benefited',
    templateUrl: './who-will-be-benefited.component.html',
    styleUrls: ['./who-will-be-benefited.component.scss'],
})
export class WhoWillBeBenefitedComponent {
    @Input() dataFromParent: any;
    id;
    benefits;
    constructor(private translateService:TranslateService) {}

    ngOnChanges() {
        this.benefits = this.dataFromParent?.benefits;
        if(this.benefits?.length>0 && this.translateService.getDefaultLang()==='bn')
        {
            this.benefits.forEach(b => {
                b.beneficiary_name = b.beneficiary_name_bangla;
            });
        }
    }

}
