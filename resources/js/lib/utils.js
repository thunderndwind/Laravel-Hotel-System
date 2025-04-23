import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...args) {
  return args.filter(Boolean).join(' ')
}

export function valueUpdater(updaterOrValue, ref) {
  ref.value =
    typeof updaterOrValue === 'function'
      ? updaterOrValue(ref.value)
      : updaterOrValue;
}
