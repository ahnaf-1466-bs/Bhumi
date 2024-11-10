import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CourseSingleFeatureComponent } from '../course-single-feature/course-single-feature.component';

import { CourseFeaturesComponent } from './course-features.component';

describe('CourseFeaturesComponent', () => {
  let component: CourseFeaturesComponent;
  let fixture: ComponentFixture<CourseFeaturesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ CourseFeaturesComponent , CourseSingleFeatureComponent],
    })
    .compileComponents();

    fixture = TestBed.createComponent(CourseFeaturesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
