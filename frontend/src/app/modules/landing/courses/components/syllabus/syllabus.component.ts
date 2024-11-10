import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-syllabus',
    templateUrl: './syllabus.component.html',
    styleUrls: ['./syllabus.component.scss'],
})
export class SyllabusComponent {
    @Input() mobile: boolean;
    @Input() dataFromParent: any;

    constructor(private translateService:TranslateService) {}

    syllabuspdf;
    syllabus_details;

    ngOnChanges() {
        this.syllabuspdf = this.dataFromParent?.syllabuspdf;
        this.syllabus_details = this.dataFromParent?.syllabus_details;

        // change heading to bangla incase lang is set to 'bn'
        if(this.syllabus_details?.length>0 && this.translateService.getDefaultLang()==='bn') 
        {
            this.syllabus_details.forEach(s => {
                s.syllabusheading=s.syllabusheading_bangla;
                s.syllabusbody = s.syllabusbody_bangla;
            });
        }
    }
}
