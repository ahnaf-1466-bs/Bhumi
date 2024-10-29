import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PotentialsComponent } from './potentials.component';

describe('PotentialsComponent', () => {
  let component: PotentialsComponent;
  let fixture: ComponentFixture<PotentialsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PotentialsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PotentialsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
