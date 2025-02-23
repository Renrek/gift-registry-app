import React from "react";
import Chip from '@mui/material/Chip';

export interface BooleanChipProps {
  value: boolean;
  trueConfig: {
    color: 'default' | 'primary' | 'secondary' | 'error' | 'info' | 'success' | 'warning';
    text: string;
  };
  falseConfig: {
    color: 'default' | 'primary' | 'secondary' | 'error' | 'info' | 'success' | 'warning';
    text: string;
  };
}

export const BooleanChip: React.FC<BooleanChipProps> = ({ value, trueConfig, falseConfig }) => {
  const config = value ? trueConfig : falseConfig;

  return (
    <Chip
      label={config.text}
      color={config.color}
    />
  );
};