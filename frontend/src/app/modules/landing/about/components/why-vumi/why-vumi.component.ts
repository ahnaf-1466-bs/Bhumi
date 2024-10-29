import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'app-why-vumi',
    templateUrl: './why-vumi.component.html',
    styleUrls: ['./why-vumi.component.scss'],
})
export class WhyVumiComponent implements OnInit {
    @Input() whyVumi: string;
    @Input() whyVumi_bn: string;

    isBengali: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }
}
