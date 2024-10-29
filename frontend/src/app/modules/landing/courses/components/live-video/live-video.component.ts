import { Component, Input } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CourseDetailsService } from '../../course-details/course-details.service';

@Component({
    selector: 'app-live-video',
    templateUrl: './live-video.component.html',
    styleUrls: ['./live-video.component.scss'],
})
export class LiveVideoComponent {
    @Input() mobile: boolean;
    @Input() dataFromParent: any;
    constructor(
        private _getCoursesService: CourseDetailsService,
        private route: ActivatedRoute
    ) {}

    id;
    description;
    descriptionURL;
    descriptionSize;
    showComponent: boolean = false;

    ngOnChanges() {
        this.descriptionSize = this.dataFromParent?.description?.length;
        if (this.descriptionSize > 1) {
            this.showComponent = true;
            this.description = this.dataFromParent?.description[1];
            this.descriptionURL = this.description?.description_url;
        }
    }
}
