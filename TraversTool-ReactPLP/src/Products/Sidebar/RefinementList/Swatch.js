import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const Swatch = props => {
  const { item, currentRefinement, swatchImages, refine, createURL, key } = props;

  const swatchActiveClass = classNames({
    'active': currentRefinement.find(refinement => refinement == item.label),
  });

  return (
    swatchImages.color.map(color =>
      item.label == color.value && (
        <li key={key}>
          <a
            href={createURL(color.value)}
            rel="nofollow"
            onClick={event => {
              event.preventDefault();
              refine(item.value);
            }}
          >
            <img className={swatchActiveClass} key={color.value} src={color.image_url} alt={color.value} />
            <p>{`${color.value} (${item.count})`}</p>
          </a>
        </li>
      )
    )
  );
}

Swatch.propTypes = {
  createURL: PropTypes.func.isRequired,
  currentRefinement: PropTypes.object.isRequired,
  isFromSearch: PropTypes.bool.isRequired,
  item: PropTypes.object.isRequired,
  key: PropTypes.string.isRequired,
  refine: PropTypes.func.isRequired,
  swatchImages: PropTypes.string.isRequired,
};

export default Swatch;
