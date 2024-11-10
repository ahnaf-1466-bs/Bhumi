import { Component, Input } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { TranslateService } from '@ngx-translate/core';

@Component({
    selector: 'app-course-overview',
    templateUrl: './course-overview.component.html',
    styleUrls: ['./course-overview.component.scss'],
})
export class CourseOverviewComponent {
    @Input() mobile: boolean;
    @Input() dataFromParent: any;

    constructor(private sanitizer: DomSanitizer,
        private translateService:TranslateService
        ) {}

    description;
    descriptionURL;
    embedVideoURL!:any;

    ngOnChanges() {
        if(this.dataFromParent?.description?.length>0){
            this.description = this.dataFromParent?.description[0];
        }
        
        this.descriptionURL = this.description?.description_url;
        
        if(this.description?.file_url && this.description?.file_url.trim().length>0)
        {
            this.embedVideoURL = this.sanitizer.bypassSecurityTrustResourceUrl(this.description?.file_url);
        }

        if(this.translateService.getDefaultLang()==='bn')
        {
            const bengaliDescription = this.description?.short_description_bangla;
            this.description.short_description = bengaliDescription?.length>0 ? bengaliDescription : '*';
        }
    }
}
