import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LearnFromSlideComponent } from './learn-from-slide.component';

describe('LearnFromSlideComponent', () => {
  let component: LearnFromSlideComponent;
  let fixture: ComponentFixture<LearnFromSlideComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ LearnFromSlideComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(LearnFromSlideComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
