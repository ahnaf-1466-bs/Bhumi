import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExpertsMindsComponent } from './experts-minds.component';

describe('ExpertsMindsComponent', () => {
  let component: ExpertsMindsComponent;
  let fixture: ComponentFixture<ExpertsMindsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ExpertsMindsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ExpertsMindsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
