import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ForWhomComponent } from './for-whom.component';

describe('ForWhomComponent', () => {
  let component: ForWhomComponent;
  let fixture: ComponentFixture<ForWhomComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ForWhomComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ForWhomComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
