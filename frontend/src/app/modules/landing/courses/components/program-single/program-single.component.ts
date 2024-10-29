import { Component, Input } from '@angular/core';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { ActivityApiService } from 'app/modules/landing/pdf-activity/services/activity-api.service';
@Component({
    selector: 'app-program-single',
    templateUrl: './program-single.component.html',
    styleUrls: ['./program-single.component.scss'],
})
export class ProgramSingleComponent {
    constructor(
        private _router: Router,
        private route: ActivatedRoute,
        private _pdf: ActivityApiService,
        private translateService: TranslateService
    ) {}

    @Input() fee: string;
    @Input() program_pdf: any;
    @Input() enrolled: boolean;
    @Input() header_month: string;
    @Input() program_dates: any[];
    @Input() program_year: string;
    @Input() course_length: string;
    @Input() result_from_parent: any;
    @Input() application_deadline: string;
    @Input() next_batch_status_code: number;
    @Input() program_details:any;
    @Input() program_name:any;

    id;
    modules;
    contents;
    response;
    activityId;
    moduleName;
    moduleType;

    endIn;
    result;
    endDate;
    nextbatch: boolean = false;

    ngOnInit() {        
        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
            localStorage.setItem('ID', this.id);
        });

        this._pdf.getActivities(this.id).subscribe((response) => {
            this.response = response[1];
            this.modules = this.response.modules;
            if (this.modules && this.modules.length > 0) {
                const firstModule = this.modules[0];
                if (
                    firstModule &&
                    firstModule.contents &&
                    firstModule.contents.length > 0
                ) {
                    this.activityId = firstModule.id;
                    localStorage.setItem('activityId', this.activityId);
                    this.moduleName = firstModule.modname;
                    localStorage.setItem('moduleName', this.moduleName);
                    this.contents = firstModule.contents[0];
                    this.moduleType = this.contents.mimetype;
                    localStorage.setItem('moduleType', this.moduleType);
                }
            }
        });
    }

    ngOnChanges() {
        this.result = this.result_from_parent;
        this.endDate = this.result?.enddate;
        this.endIn = this.endDate * 1000 - Date.now();
        if (this.next_batch_status_code == 200) {
            this.nextbatch = true;
        }

        // convert to bangla if language is set to 'bn'
        if (this.translateService.getDefaultLang() === 'bn') {
            if (this.program_name?.length>0) {
                this.program_name.forEach(p => {
                    p.name = p.name_bangla;
                });
            }
            if (this.program_dates?.length>0) {
                this.program_dates.forEach(p => {
                    p.programdate = p.programdate_bangla;
                });
            }
            if (this.program_details?.length>0) {
                this.program_details.forEach(p => {
                    p.name = p.name_bangla;
                    p.value = p.value_bangla;
                });
            }
        }
    }

    goto() {
        this._router.navigate(['/enrollment', this.id]);
    }
}
