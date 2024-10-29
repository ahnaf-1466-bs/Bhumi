import { Component } from '@angular/core';
import { Director } from '../models/director';
import { Founder } from '../models/founder';
import { Instructor } from '../models/instructor';
import { Operation } from '../models/operation';
import { TeamsApiService } from '../services/teams-api.service';

import SwiperCore, {
    A11y,
    Autoplay,
    Navigation,
    Pagination,
    Scrollbar,
} from 'swiper';

SwiperCore.use([Navigation, Pagination, Scrollbar, A11y, Autoplay]);

@Component({
    selector: 'app-teams',
    templateUrl: './teams.component.html',
    styleUrls: ['./teams.component.scss'],
    // encapsulation: ViewEncapsulation.None
})
export class TeamsComponent {
    directorList: Director[] = [];
    founderList: Founder[] = [];
    isShowFounderList: boolean = false;
    instructorList: Instructor[] = [];
    isShowInstructorList: boolean = false;
    operationList: Operation[] = [];
    isShowOperationList: boolean = false;
    mxTierDirector: number = 1;
    mxTierOperation: number = 1;
    isDataLoaded: boolean = false;

    constructor(private teamsApi: TeamsApiService) {}

    ngOnInit() {
        this.teamsApi.getTeamPageData().subscribe((res: any) => {
            if (res.directorlist) {
                this.directorList = res.directorlist;
                for (let director of this.directorList) {
                    this.mxTierDirector = Math.max(
                        this.mxTierDirector,
                        director.tier
                    );
                }
                this.isDataLoaded = true;
            }

            if (res.founderlist) {
                this.founderList = res.founderlist;
                this.isShowFounderList =
                    this.founderList.length > 0 ? true : false;
                this.founderList.sort((a, b) => a.tier - b.tier);
            }

            if (res.instructorlist) {
                this.instructorList = res.instructorlist;
                this.isShowInstructorList =
                    this.instructorList.length > 0 ? true : false;
                this.instructorList.sort((a, b) => a.tier - b.tier);
            }

            if (res.operationteamlist) {
                this.operationList = res.operationteamlist;
                this.isShowOperationList =
                    this.operationList.length > 0 ? true : false;
                for (let operation of this.operationList) {
                    this.mxTierOperation = Math.max(
                        this.mxTierOperation,
                        operation.tier
                    );
                }
            }
        });
    }
}
