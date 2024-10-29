import { ComponentFixture, TestBed } from '@angular/core/testing';

import { WishlistedCourseComponent } from './wishlisted-course.component';

describe('WishlistedCourseComponent', () => {
  let component: WishlistedCourseComponent;
  let fixture: ComponentFixture<WishlistedCourseComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ WishlistedCourseComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(WishlistedCourseComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
