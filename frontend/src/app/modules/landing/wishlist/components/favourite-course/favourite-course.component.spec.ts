import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FavouriteCourseComponent } from './favourite-course.component';

describe('FavouriteCourseComponent', () => {
  let component: FavouriteCourseComponent;
  let fixture: ComponentFixture<FavouriteCourseComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ FavouriteCourseComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FavouriteCourseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
