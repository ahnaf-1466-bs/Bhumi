import { Component, Input } from '@angular/core';

@Component({
    selector: 'app-course-features',
    templateUrl: './course-features.component.html',
    styleUrls: ['./course-features.component.scss'],
})
export class CourseFeaturesComponent {
    @Input() dataFromParent: any;

    features;

    ngOnChanges() {
        this.features = this.dataFromParent?.features;
        console.log("this.features",this.features);
        
    }
    constructor() {}
}
