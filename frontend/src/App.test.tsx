import React from 'react';
import { render, screen } from '@testing-library/react';
import App from './App';

test('renders login page when unauthenticated', () => {
  render(<App />);
  const loginHeading = screen.getByText(/HMIS Login/i);
  expect(loginHeading).toBeInTheDocument();
});
