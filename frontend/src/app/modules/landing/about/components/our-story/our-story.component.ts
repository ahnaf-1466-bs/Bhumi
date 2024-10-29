import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'app-our-story',
    templateUrl: './our-story.component.html',
    styleUrls: ['./our-story.component.scss'],
})
export class OurStoryComponent implements OnInit {
    @Input() story: string;
    @Input() story_bn: string;

    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }
}
