import { Component, Input } from '@angular/core';
import { ActivatedRoute, ParamMap } from '@angular/router';
import { CourseDetailsService } from '../../course-details/course-details.service';

@Component({
    selector: 'app-program-structure',
    templateUrl: './program-structure.component.html',
    styleUrls: ['./program-structure.component.scss'],
})
export class ProgramStructureComponent {
    @Input() mobile: boolean;
    @Input() enrolled: boolean;
    @Input() dataFromParent: any;
    @Input() dataFromParent2: any;

    constructor(
        private route: ActivatedRoute,
        private _getCoursesService: CourseDetailsService
    ) {}

    id;

    resultFromParent;
    programstructure;
    programDate;
    programpdf;
    deadline;
    length;
    fee;

    programDate_next_batch;
    program_pdf_next_batch;
    deadline_next_batch;
    length_next_batch;
    fee_next_batch;
    status_code;
    batch_id;

    nextBatchStatus: boolean = false;
    programDetails:any;
    programName:any;

    ngOnChanges() {
        this.programstructure = this.dataFromParent?.programstructure;
        this.programDate = this.dataFromParent?.all_program_dates;
        this.programpdf = this.dataFromParent?.programpdf;

        // no longer needed for app-program-single
        this.deadline = this.dataFromParent?.deadline;

        this.resultFromParent = this.dataFromParent2;

        // no longer needed for app-program-single
        this.length = this.dataFromParent?.length;
        
        // no longer needed for app-program-single
        this.fee = this.dataFromParent?.fee;

        // new
        this.programDetails = this.dataFromParent?.programdetails;
        
        // new
        this.programName = this.dataFromParent?.programname;
        
    }

    ngOnInit() {
        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
        });

        let body2 = [
            { wsfunction: 'local_preregistration_get_batch_data_by_courseid' },
            { courseid: this.id },
        ];
        this._getCoursesService.getNextBatch(body2).subscribe((res: any) => {
            if (res.statuscode == 200) {
                this.nextBatchStatus = true;
            }
            this.programDate_next_batch = res?.all_program_dates;
            this.program_pdf_next_batch = res?.programpdf;
            this.deadline_next_batch = res?.deadline;
            this.length_next_batch = res?.length;
            this.status_code = res?.statuscode;
            this.fee_next_batch = res?.fee;
            this.batch_id = res?.batchid;
        });
    }
}
