import { Component, Input } from '@angular/core';
import { Director } from '../../models/director';

@Component({
    selector: 'app-director',
    templateUrl: './director.component.html',
    styleUrls: ['./director.component.scss'],
})
export class DirectorComponent {
    @Input() directorList: Director[];
    @Input() isDataLoaded: boolean;
    @Input() mxTierDirector: number;
    isBengali: boolean = false;

    tier: Director[][] = [];
    rows: number = 0;
    isShowMessage: boolean = false;

    ngOnInit() {
        if (localStorage.getItem('lang') === 'bn') {
            this.isBengali = true;
        } else {
            this.isBengali = false;
        }
    }

    ngOnChanges() {
        for (let director of this.directorList) {
            if (!director.directordeg_bn) {
                director.directordeg_bn = director.directordeg;
            }
            if (!director.directorname_bn) {
                director.directorname_bn = director.directorname;
            }
        }

        if (this.directorList?.length == 0) {
            this.isShowMessage = true;
        }

        if (this.directorList.length > 0) {
            this.isShowMessage = false;
            this.rows = this.mxTierDirector;

            for (let i = 0; i < this.rows; i++) {
                this.tier[i] = [];
                for (let director of this.directorList) {
                    if (director.tier == i + 1) this.tier[i].push(director);
                }
            }
        }
    }

    public getNumberArray(length: number): number[] {
        return Array(length)
            .fill(0)
            .map((x, i) => i + 1);
    }
}
