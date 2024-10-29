import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CourseSingleFeatureComponent } from './course-single-feature.component';

describe('CourseSingleFeatureComponent', () => {
  let component: CourseSingleFeatureComponent;
  let fixture: ComponentFixture<CourseSingleFeatureComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CourseSingleFeatureComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CourseSingleFeatureComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
