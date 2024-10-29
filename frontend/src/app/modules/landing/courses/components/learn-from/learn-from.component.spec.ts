import { ComponentFixture, TestBed } from '@angular/core/testing';
import { LearnFromSingleComponent } from '../learn-from-single/learn-from-single.component';

import { LearnFromComponent } from './learn-from.component';

describe('LearnFromComponent', () => {
  let component: LearnFromComponent;
  let fixture: ComponentFixture<LearnFromComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ LearnFromComponent, LearnFromSingleComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(LearnFromComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
