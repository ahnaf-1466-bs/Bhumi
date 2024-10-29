import { Component, Input } from '@angular/core';
import { ActivatedRoute, ParamMap, Router } from '@angular/router';
@Component({
    selector: 'app-program-next-bacth',
    templateUrl: './program-next-batch.component.html',
    styleUrls: ['./program-next-batch.component.scss'],
})
export class ProgramNextBatchComponent {
    @Input() next_batch_id: number;
    @Input() fee_next_batch: string;
    @Input() header_month_next_batch: string;
    @Input() program_dates_next_batch: any[];
    @Input() program_year_next_batch: string;
    @Input() course_length_next_batch: string;
    @Input() program_pdf_next_batch: string;
    @Input() application_deadline_next_batch: string;

    enrolled: boolean = false;

    constructor(private route: ActivatedRoute, private _router: Router) {}

    id;
    batch;

    ngOnInit() {
        this.route.paramMap.subscribe((params: ParamMap) => {
            this.id = +params.get('id');
        });
    }

    ngOnChanges() {
        this.batch = this.next_batch_id;
    }

    goToPreRegistration() {
        this._router.navigate([`/pre-registration`], {
            queryParams: {
                course: this.id,
                batch: this.batch,
            },
        });
    }
}
