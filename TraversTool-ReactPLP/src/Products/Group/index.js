import Description from './Description';
import PropTypes from 'prop-types';
import React, { useContext } from 'react';
import classNames from 'classnames';
import LazyLoad from 'react-lazyload';
import memoize from 'memoizee';
import { ProductsContext } from '../ProductsContext';

const makeRefinements = memoize((refinements, attribute, value) => {
  return { ...refinements, [attribute]: value };
});

const childGroups = memoize((groups) => groups.slice(1));

/*
  This is a recursive component. Travers can select up to 3 grouping attributes,
  each of which is a Group. If we have Size, Color, and Material as the
  grouping attributes in that order, we will want to display that as a series
  of cascading headers.

  Level 1: Size:"2" has 500 products
  Level 2: Size:"2" + Color:"Red" has 100 products
  Level 3: Size:"2" + Color:"Red" + "Material:"Metal" has 10 products

  The Level 3 products are the only ones we want to display. This component
  both renders the headers and narrows down the products to show by group level
*/

const Group = ({
  children,
  descriptions = {},
  facetCounts,
  groupImages,
  groupName,
  groups,
  level = 1,
  refinements = [],
  valueKey = '',
  nbHits = '',
  searchState,
  setNbHits = () => {},
  view
}) => {
  if (!groups.length) {
    return children({ groupName, refinements, setNbHits, valueKey });
  }

  const { attribute } = groups[0];
  const groupCounts = facetCounts[attribute] || {};
  const Heading = `h${level + 1}`;
  const containerClassNames = classNames({
    groups: level === 1,
  });
  const {position} = useContext(ProductsContext);
  
  return (
    <div className={containerClassNames} style={{display:"flex",flexDirection:"column"}}>
      {groups[0].values.map(value => {
        if (!groupCounts[value] || groupCounts[value] < 1) {
          return null;
        }
        const className = classNames('group', `group--level-${level}`);
        let key = `${attribute}::${value}`;
        if (valueKey) {
          key = `${valueKey}--${attribute}::${value}`;
        }

        const allRefinements = makeRefinements(refinements, attribute, value);

        return (
          <div key={`${key}-lazy`} style={position && position[value]  ? {order:position[value]+""} : {}}>
          <LazyLoad height={200} offset={1500}>
            <div className={className} key={key}>
              {nbHits[key] > 0 && (
                <React.Fragment>
                  <div className="group-header">
                    {groupImages[value] && (
                      <img className="grouping-image" src={groupImages[value]} alt=""></img>
                    )}
                    <Heading>{value}</Heading>
                  </div>
                  <Description
                    attribute={attribute}
                    descriptions={descriptions}
                    value={value}
                  />
                </React.Fragment>
              )}
              <Group
                descriptions={descriptions}
                facetCounts={facetCounts}
                groupImages={groupImages}
                groupName={value}
                groups={childGroups(groups)}
                level={level + 1}
                nbHits={nbHits}
                refinements={allRefinements}
                setNbHits={setNbHits}
                valueKey={key}
              >
                {children}
              </Group>
            </div>
          </LazyLoad>
          </div>
        );
      })}
    </div>
  );
}

Group.propTypes = {
  children: PropTypes.func.isRequired,
  descriptions: PropTypes.object,
  facetCounts: PropTypes.object.isRequired,
  groupImages: PropTypes.object,
  groupName: PropTypes.string,
  groups: PropTypes.array.isRequired,
  level: PropTypes.number,
  nbHits: PropTypes.object,
  refinements: PropTypes.object,
  searchState: PropTypes.object,
  setNbHits: PropTypes.func.isRequired,
  valueKey: PropTypes.string,
  view: PropTypes.string,
};

export default Group;
