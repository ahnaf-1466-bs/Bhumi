import { ComponentFixture, TestBed } from '@angular/core/testing';

import { KeyPartnersComponent } from './key-partners.component';

describe('KeyPartnersComponent', () => {
  let component: KeyPartnersComponent;
  let fixture: ComponentFixture<KeyPartnersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ KeyPartnersComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(KeyPartnersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
