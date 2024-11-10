import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ProgramSlideComponent } from './program-slide.component';

describe('ProgramSlideComponent', () => {
  let component: ProgramSlideComponent;
  let fixture: ComponentFixture<ProgramSlideComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ProgramSlideComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ProgramSlideComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
