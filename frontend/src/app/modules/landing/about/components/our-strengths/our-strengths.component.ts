import { Component, Input } from '@angular/core';

@Component({
    selector: 'app-our-strengths',
    templateUrl: './our-strengths.component.html',
    styleUrls: ['./our-strengths.component.scss'],
})
export class OurStrengthsComponent {
    @Input() ourStrengths: any[];
    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }
}
