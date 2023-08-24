import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ListDiskonComponent } from './list-diskon.component';

describe('ListDiskonComponent', () => {
  let component: ListDiskonComponent;
  let fixture: ComponentFixture<ListDiskonComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ListDiskonComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ListDiskonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
