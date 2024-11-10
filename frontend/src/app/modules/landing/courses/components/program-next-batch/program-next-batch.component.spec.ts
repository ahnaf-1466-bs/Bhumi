import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProgramNextBatchComponent } from './program-next-batch.component';

describe('ProgramNextBatchComponent', () => {
    let component: ProgramNextBatchComponent;
    let fixture: ComponentFixture<ProgramNextBatchComponent>;

    beforeEach(async () => {
        await TestBed.configureTestingModule({
            declarations: [ProgramNextBatchComponent],
        }).compileComponents();

        fixture = TestBed.createComponent(ProgramNextBatchComponent);
        component = fixture.componentInstance;
        fixture.detectChanges();
    });

    it('should create', () => {
        expect(component).toBeTruthy();
    });
});
