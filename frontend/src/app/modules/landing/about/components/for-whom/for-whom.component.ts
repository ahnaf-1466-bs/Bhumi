import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'app-for-whom',
    templateUrl: './for-whom.component.html',
    styleUrls: ['./for-whom.component.scss'],
})
export class ForWhomComponent implements OnInit {
    @Input() forWhom: any[];
    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }
}
