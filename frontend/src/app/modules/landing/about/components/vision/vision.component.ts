import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'app-vision',
    templateUrl: './vision.component.html',
    styleUrls: ['./vision.component.scss'],
})
export class VisionComponent implements OnInit {
    @Input() vision: string;
    @Input() vision_bn: string;
    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }
}
